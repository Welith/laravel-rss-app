import React, {Component} from "react";
import {Link} from 'react-router-dom';
import swal from "sweetalert";

class ShowFeed extends Component {


    feed_id = this.props.match.params.id;

    state = {
        title: '',
        link: '',
        source: '',
        source_url: '',
        publish_date: '',
        description: ''
    }

    handleInput = (e) => {
        this.setState({
            [e.target.name]: e.target.value
        });
    }

    async componentDidMount(){

        const res = await axios.get(`/api/feeds/${this.feed_id}`)

        if (res.data.status === 200) {

            this.setState({

                title: res.data.feed.title,
                link: res.data.feed.link,
                source: res.data.feed.source,
                source_url: res.data.feed.source_url,
                publish_date: res.data.feed.publish_date,
                description: res.data.feed.description
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

    render() {

        return(
            <div className="container align-items-center justify-content-center">
                <div className="row align-items-center justify-content-center">
                    <div className="col-md-6 align-items-center justify-content-center">
                        <div className="card">
                            <div className="card-header text-center">
                                <h4>Feed # {this.feed_id}
                                    <Link to={'/'} className="btn btn-primary btn-sm float-right"><i className="fas fa-long-arrow-alt-left"></i> Back</Link>
                                </h4>
                            </div>
                            <div className="card-body">
                                    <div className="form-group mb-3">
                                        <label>Title</label>
                                        <label className="form-control">{this.state.title}</label>
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Link</label>
                                        <label className="form-control">{this.state.link}</label>
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Source</label>
                                        <label className="form-control">{this.state.source}</label>
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Source URL</label>
                                        <label className="form-control">{this.state.source_url}</label>
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Publish Date</label>
                                        <label className="form-control">{this.state.publish_date}</label>
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Description</label>
                                        <label className="form-control">{this.state.description}</label>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default ShowFeed;


