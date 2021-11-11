import React, {Component} from "react";
import {Link} from 'react-router-dom';
import swal from "sweetalert";
import Pagination from "react-js-pagination";

class Feed extends Component {

    state = {
        feeds: [],
        loading: true,
        activePage: 1,
        itemsCountPerPage: 1,
        totalItemsCount: 1,
        title: null,
        link: null,
        publish_date_from: null,
        publish_date_to: null,
        urls: {"urls": (process.env.MIX_RSS_FEED_ARRAY).split(",")}

    }

    componentDidMount = async (pageNum = 1) => {

       await this.getUserData(pageNum)
    }

    filter = async (e) => {

        e.preventDefault()

        await this.getUserData(this.state.activePage, this.state.link, this.state.title, this.state.publish_date_from, this.state.publish_date_to)
    }

    getUserData = async (pageNum = 1, link = null, title = null, publish_date_from = null, publish_date_to = null) => {

        const res = await axios.get(`/api/feeds/list`, {
            params: {
                page: pageNum,
                link: link,
                title: title,
                publish_date_from: publish_date_from,
                publish_date_to: publish_date_to,
            }
        });

        if (res.data.status === 200) {

            this.setState({

                feeds: res.data.feeds.data,
                loading: false,
                activePage: res.data.feeds.current_page,
                itemsCountPerPage: res.data.feeds.per_page,
                totalItemsCount: res.data.feeds.total
            })
        }
    }

    handleInput = (e) => {

        this.setState({

            [e.target.name]: e.target.value === "" ? null : e.target.value
        });
    }

    deleteFeed = async (e, id) => {

        e.preventDefault();

        const deleteButton = e.currentTarget;
        deleteButton.innerText = "Deleting";

        const res = await axios.delete(`/api/feeds/${id}/delete`);

        if (res.data.status === 200) {

            swal({
                title: "Deleted!",
                text: res.data.message,
                icon: "success",
                button: "OK",
            });

            deleteButton.closest("tr").remove()
        } else {

            swal({
                title: "Error!",
                text: res.data.message,
                icon: "error",
                button: "OK",
            });
        }
    }

    async fetchFeeds(e) {

        e.preventDefault();

        const res = await axios.post(`/api/feeds/fetch-go`, this.state.urls);
        console.log(res)
        if (res.data.status === 200) {

            swal({
                title: "Success!",
                text: res.data.message,
                icon: "success",
                button: "OK",
            }).then(function () {

                window.location = '/';
            });

        } else {

            swal({
                title: "Error!",
                text: res.data.message,
                icon: "error",
                button: "OK",
            });
        }
    }

    async handlePageChange(pageNumber) {

        await this.getUserData(pageNumber, this.state.link, this.state.title, this.state.publish_date_from, this.state.publish_date_to)
    }

    render() {

        let feed_HTML_TABLE;

        if (this.state.loading) {

            feed_HTML_TABLE = <tr><td colSpan="2">Loading ...</td></tr>
        } else {

            feed_HTML_TABLE = this.state.feeds.map((item) => {
                return (
                    <tr key={item.id}>
                        <td className="w-100">
                            {item.title}
                        </td>
                        <td className="w-auto d-flex justify-content-center">
                            <Link to={`/feeds/${item.id}`} className="btn btn-primary btn-sm"><i className="fas fa-eye"></i></Link>
                            &nbsp;
                            <Link to={`/feeds/${item.id}/edit`} className="btn btn-success btn-sm"><i className="fas fa-edit"></i></Link>
                            &nbsp;
                            <Link onClick={(e) => this.deleteFeed(e, item.id)} className="btn btn-danger btn-sm"><i className="fas fa-trash-alt"></i></Link>
                        </td>
                    </tr>
                );
            });
        }

        return(
            <div className="container align-items-center justify-content-center">
                <div className="row align-items-center justify-content-center">
                    <div className="col-md-12">
                        <div className="card">
                            <div className="card-header">
                                <h4 className="text-center">RSS Feed Preview
                                    <Link to={'/feeds'} className="btn btn-primary btn-sm float-right"><i className="fas fa-plus-square"></i> Add Feed</Link>
                                    <button onClick={ (e) => this.fetchFeeds(e)} className="btn btn-primary btn-sm float-left"><i className="fas fa-plus-square"></i> Fetch Feeds</button>
                                </h4>
                            </div>
                            <div className="card-header align-items-center justify-content-center">
                                <form className="form-inline" onSubmit={this.filter}>
                                    <input type="text" name="title" className="form-control m-1" value={this.state.title} onChange={this.handleInput} placeholder="Title..."/>

                                    <input type="text" className="form-control m-1" name="link" value={this.state.link} onChange={this.handleInput}  placeholder="RSS Feed..."/>

                                    <label htmlFor="publish_date_from" className="m-1">Date From:</label>
                                    <input type="datetime-local" className="form-control m-1" value={this.state.publish_date_from} onChange={this.handleInput} name="publish_date_from" id="publish_date_from"/>

                                    <label htmlFor="publish_date_to" className="m-1">Date To:</label>
                                    <input type="datetime-local" className="form-control m-1" value={this.state.publish_date_to} onChange={this.handleInput} name="publish_date_to" id="publish_date_to"/>

                                    <button type="submit" className="btn btn-primary btn-sm float-right">Filter</button>

                                </form>
                            </div>
                            <div className="card-body">
                                <table className="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th className="text-center w-100">Title</th>
                                            <th className="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {feed_HTML_TABLE}
                                    </tbody>
                                </table>
                                <div className="d-flex justify-content-center">
                                    <Pagination
                                        activePage={this.state.activePage}
                                        itemsCountPerPage={this.state.itemsCountPerPage}
                                        totalItemsCount={this.state.totalItemsCount}
                                        pageRangeDisplayed={5}
                                        onChange={this.handlePageChange.bind(this)}
                                        itemClass="page-item"
                                        linkClass="page-link"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default Feed;


