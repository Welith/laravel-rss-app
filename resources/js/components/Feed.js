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
        totalItemsCount: 1
    }

    componentDidMount = async (pageNum = 1) => {

       await this.getUserData(pageNum)
    }

    getUserData = async (pageNum = 1) => {

        const resp = await axios.get(`/api/feeds/list?page=${pageNum}`);

        if (resp.data.status === 200) {

            this.setState({

                feeds: resp.data.feeds.data,
                loading: false,
                activePage: resp.data.feeds.current_page,
                itemsCountPerPage: resp.data.feeds.per_page,
                totalItemsCount: resp.data.feeds.total
            })
        }
    }

    deleteStudent = async (e, id) => {

        const deleteButton = e.currentTarget;
        deleteButton.innerText = "Deleting";

        const resp = await axios.delete(`/api/feeds/${id}/delete`);

        if (resp.data.status === 200) {

            swal({
                title: "Deleted!",
                text: resp.data.message,
                icon: "success",
                button: "OK",
            });

            deleteButton.closest("tr").remove()
        }
    }

    async handlePageChange(pageNumber) {

        await this.getUserData(pageNumber)
    }

    render() {

        let feed_HTML_TABLE;

        if (this.state.loading) {

            feed_HTML_TABLE = <tr><td colSpan="8">Loading ...</td></tr>
        } else {

            feed_HTML_TABLE = this.state.feeds.map((item) => {
                return (
                    <tr key={item.id}>
                        <td>
                            {item.title}
                        </td>
                        <td>
                            {item.link}
                        </td>
                        <td>
                            {item.source}
                        </td>
                        <td>
                            {item.source_url}
                        </td>
                        <td>
                            {item.publish_date}
                        </td>
                        <td>
                            {item.description}
                        </td>
                        <td>
                            <Link to={`/feeds/${item.id}`} className="btn btn-success btn-sm"><i className="fas fa-edit"></i></Link>
                            &nbsp;
                            <Link onClick={(e) => this.deleteStudent(e, item.id)} className="btn btn-danger btn-sm"><i className="fas fa-trash-alt"></i></Link>
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
                                </h4>
                            </div>
                            <div className="card-body">
                                <table className="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Link</th>
                                            <th>Source</th>
                                            <th>Source URL</th>
                                            <th>Publish Date</th>
                                            <th>Description</th>
                                            <th>Actions</th>
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


